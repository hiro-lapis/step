import { AxiosResponse } from 'axios'
import axios from './baseRepository'

/**
 * FileManageRepository
 */
export class FileManageRepository {
    private readonly presignedUrlUrl = '/api/presigned-upload-url'
    private readonly presignedDownloadUrl = '/api/presigned-download-url'

    async getSignedUrl(file_name: string): Promise<AxiosResponse<PresignedUrlResponse>> {
        return await axios.post(`${this.presignedUrlUrl}`, { file_name: file_name })
    }
    async upload(presignedUrl: string, file: File|null): Promise<AxiosResponse<UploadResponse>> {
        let formData = new FormData()
        formData.append('file', file!)
        const config = { headers: {
            'Content-Type': file!.type
         } }
        return await axios.put(`${presignedUrl}`, file, config)
    }
    async download(filePath: string): Promise<AxiosResponse<DownloadResponse>> {
        return await axios.get(`${this.presignedDownloadUrl}?file_path=${filePath}`)
    }

    // async getSignedUrl(file: File): Promise<AxiosResponse<PresignedUrlResponse>> {
    //     let formData = new FormData()
    //     formData.append('file', file, file.name)
    //     return await axios.post(`${this.presignedUrlUrl}`, formData)
    // }
    // async upload(presignedUrl: string, file: File): Promise<AxiosResponse<UploadResponse>> {
    //     const config = { headers: { 'Content-Type': file.type } }
    //     return await axios.put(`${presignedUrl}`, file, config)
    // }
}

type PresignedUrlResponse = {
    upload_path: string,
    presigned_url: string,
}

type UploadResponse = {
}
type DownloadResponse = {
    presigned_url: string,
}
